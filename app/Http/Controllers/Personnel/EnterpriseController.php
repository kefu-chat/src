<?php

namespace App\Http\Controllers\Personnel;

use Aoxiang\Pca\Models\ProvinceCityArea;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    /**
     * 企业资料
     */
    public function show(Request $request)
    {
        return response()->success(['enterprise' => $this->user->enterprise]);
    }

    /**
     * 企业资料
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string'],
            'serial' => ['nullable', 'string'],
            'profile' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'geographic' => ['nullable', 'array'],
            'geographic.province.key' => ['nullable', 'integer'],
            'geographic.city.key' => ['nullable', 'integer'],
            'geographic.area.key' => ['nullable', 'integer'],
            'geographic.street.key' => ['nullable', 'integer'],
        ]);
        $enterprise = $this->user->enterprise;
        $enterprise->fill($request->only([
            'name',
            'serial',
            'profile',
            'country',
            'address',
            'phone',
            'geographic',
        ]));
        $enterprise->save();
        return response()->success(['enterprise' => $enterprise]);
    }

    public function search(Request $request)
    {
        $request->validate(['name' => ['required', 'string',]]);

        $search = (new Client())->post('https://aiqicha.baidu.com/smart/sugListAjax', [
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::FORM_PARAMS => [
                'q' => $request->input('name'),
            ],
            RequestOptions::HEADERS => [
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.14(0x17000e2e) NetType/WIFI Language/zh_CN',
                'Referer' => 'https://servicewechat.com/wxbb128540d8b3b705/10/page-frame.html',
            ],
        ]);
        $search = json_decode($search->getBody());
        if ($search->status !== 0) {
            abort(400, $search->msg);
        }

        return response()->success([
            'list' => collect($search->data->queryList)->map(fn($item) => ($item->resultStr = strip_tags($item->resultStr)) ? $item : $item),
        ]);
    }

    public function searchDetail(Request $request)
    {
        $request->validate(['pid' => ['required', 'string',]]);

        $info = (new Client())->get('https://aiqicha.baidu.com/smart/basicAjax?' . http_build_query([
            'pid' => $request->input('pid'),
        ]), [
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::HEADERS => [
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.14(0x17000e2e) NetType/WIFI Language/zh_CN',
                'Referer' => 'https://servicewechat.com/wxbb128540d8b3b705/10/page-frame.html',
            ],
        ]);
        $info = json_decode($info->getBody());
        if ($info->status !== 0) {
            abort(400, $info->msg);
        }

        $gis = (new Client())->get('https://apis.map.qq.com/ws/geocoder/v1/?' . http_build_query([
            'address' => $info->data->regAddr,
            'key' => 'ZFRBZ-XKKR5-PVHIY-QE65O-SKO7Q-6SFHQ',
        ]));
        $gis = json_decode($gis->getBody());
        $info->data->province = $gis->result->address_components->province;
        $info->data->city = $gis->result->address_components->city;
        $info->data->area = $gis->result->address_components->district;
        $info->data->street = $gis->result->address_components->street;
        $info->data->province_code = data_get(ProvinceCityArea::where('name', 'LIKE', preg_replace('/(省|市|特别行政区|自治区)/', '', $info->data->province))->first(), 'id');
        $info->data->city_code = data_get(ProvinceCityArea::where('name', 'LIKE', $info->data->city)->first(), 'id');
        $info->data->area_code = data_get(ProvinceCityArea::where('name', 'LIKE', $info->data->area)->first(), 'id');
        $info->data->street_code = data_get(ProvinceCityArea::where('name', 'LIKE', $info->data->street)->first(), 'id');
        if (!$info->data->city_code && $info->data->area_code) {
            $info->data->city_code = $info->data->area_code;
            $info->data->city = $info->data->area;
        }

        return response()->success($info->data);
    }
}
