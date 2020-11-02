cw=$(pwd) && \
mkdir -p ../laravel-echo-server && \
cd ../laravel-echo-server && \
git init && \
git pull https://github.com/fastsupport-cn/laravel-echo-server.git master && \
npm i && \
tsc && \
cd "$cw" && \
node ../laravel-echo-server/bin/server.js start
