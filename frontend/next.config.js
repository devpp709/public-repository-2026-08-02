/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: false,
    turbopack: {},

    async rewrites() {
        return [
            {
                source: '/api/:path*',
                destination: 'http://nginx:80/api/:path*',
            },
        ];
    }
};

module.exports = nextConfig;