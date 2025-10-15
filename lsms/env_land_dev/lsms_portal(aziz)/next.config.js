const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');

const createNextIntlPlugin = require('next-intl/plugin');
const withNextIntl = createNextIntlPlugin();

/** @type {import("next").NextConfig} **/

const nextConfig = {
  reactStrictMode: false,
  productionBrowserSourceMaps: false,
  pageExtensions: ['js', 'jsx', 'mdx'],
  swcMinify: true,  // Use SWC for faster minification

  env: {
    USERNAME: 'samsul',
    PASSWORD: 'mhl@123@2019',
    BASE_TOKEN_URL: 'https://devldtax.mysoftheaven.com/api/token',
    BASE_URL: 'https://devldtax.mysoftheaven.com',
    PAYMENT_BASE_URL: 'https://dakhila-v2.limslrb.com',
    BASE_URL_V1: 'https://ldtv2dev.apimanager.mysoftheaven.com/api/v1',
    BASE_URL_V1_BASE: 'https://ldtv2dev.apimanager.mysoftheaven.com',
    BASE_PAYMENT_URL: 'https://devldtax.mysoftheaven.com/paymentapi',
    LDTAX_PORTAL_BASE: 'http://localhost:3000',
    PORTAL_API: 'http://lsms-portal-api.com/api',
    PORTAL_ASSET: 'http://lsms-portal-api.com',
    LSG_PORTAL_API: 'https://lsg-portal-api.mysoftheaven.com',
    SSO_URL: 'http://127.0.0.1:8080',
    SSO_LIVE_URL: 'https://idp-devsso.land.gov.bd',
    SSO_CLIENT_ID: '9aa4673b-6923-4855-9fc9-ff56b04dbdf3',
    SSO_SECRET: 'DfSqhod28T03Kjfz3dkbaP1GWj0apPaHt6gBxOQ1',
    SSO_AUTHORIZE_URL: 'http://127.0.0.1:8080/oauth/authorize?',
    SSO_REDIRECT_URI: 'http://localhost:3000/api/callback',
    SSO_TOKEN_URL: 'http://127.0.0.1:8080/oauth/token',
    LOGIN_URL: '/sso',
    OFFICE_URL: 'http://127.0.0.1:8000',
    OFFICE_LOGIN_URL: 'http://127.0.0.1:8000/login',
    ORG_API_URL: 'https://ldtaxorgapi.mysoftheaven.com/api',
    ORG_USERNAME: 'LdtaxOrg1',
    ORG_PASSWORD: 'api@ldtaxOrg@2024',
    CHATBOT_URL: 'http://127.0.0.1:5000/chat',
    REDIS_URL: 'redis://default:AcLbAAIjcDEzNjFiZDNlYjdhYjU0ZGQ1OWZhZTUzN2MwZjNkMmVhY3AxMA@magical-mongoose-49883.upstash.io:6379'
  },
  webpack: (config) => {
    config.plugins.push(
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
      }),
    );
    // Enable filesystem caching to speed up reloads
    config.cache = {
      type: 'filesystem',
      buildDependencies: {
        config: [__filename],
      },
    };
    
    config.watchOptions = {
      aggregateTimeout: 300, // Delay the rebuild after the first change
      poll: 1000,            // Check for changes every second
      ignored: /node_modules/, // Ignore node_modules
    };
    
    // Configure Webpack's SplitChunks to split up large bundles
    config.optimization = {
      ...config.optimization,
      splitChunks: {
        chunks: 'all',
        minSize: 20000,
        maxSize: 70000, // Limit chunk size to improve load times
      },
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            compress: { drop_console: true }, // Remove console logs in production
            output: {
              comments: false, // Remove comments for smaller files
            },
          },
        }),
      ],
      moduleIds: 'deterministic', // Consistent hashing for stable module IDs
      runtimeChunk: 'single', // Separate runtime code into a single file
    };
    
    return config;
  },
  images: {
    minimumCacheTTL: 60,
    dangerouslyAllowSVG: true,
    contentDispositionType: 'attachment',
    domains: ['localhost'],
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'devldtax-portal-admin-api.mysoftheaven.com',
      },
      {
        protocol: 'http',
        hostname: 'lsms-portal-api.com',
      },
      {
        protocol: 'https',
        hostname: 'land.gov.bd',
      },
      {
        protocol: 'https',
        hostname: 'mysoftheaven.com',
      },
      
      {
        protocol: 'https',
        hostname: 'beta-idp.stage.mygov.bd',
      },

      {
        protocol: 'https',
        hostname: 'idp-devsso.land.gov.bd',
      },
      {
        protocol: 'https',
        hostname: 'ldtax-portal-admin-stage.land.gov.bd',
      },
      
    ]
  },
  async headers() {
    return [
      {
        source: '/',
        headers: [
          {
            key: 'Cache-Control',
            value: 'max-age=9999999999, must-revalidate',
          }
        ],
      },
    ]
  },
  experimental: {
    serverActions: true
  }
}

// module.exports = nextConfig;
module.exports = withNextIntl(nextConfig);

