const nextConfig = {
    reactStrictMode: false,

    allowedDevOrigins: ['31.31.201.163'],

    async rewrites() {
        return [
            {
                source: '/api/:path*',
                destination: 'http://nginx:80/api/:path*',
            },
        ];
    },
};

module.exports = nextConfig;