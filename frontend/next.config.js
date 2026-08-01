/** @type {import('next').NextConfig} */
const nextConfig = {
    swcMinify: false,
    reactStrictMode: false,
    webpack: (config) => {
        return config;
    },
};

module.exports = nextConfig;