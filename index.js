const path = require('path');
const Encore = require('@symfony/webpack-encore');

class CommerceWeaversSyliusTpay {
  static getWebpackShopConfig(rootDir) {
    Encore
      .setOutputPath('public/build/commerce-weavers/tpay/shop')
      .setPublicPath('/build/commerce-weavers/tpay/shop')
      .addEntry('commerce-weavers-sylius-tpay-shop-checkout-complete-entry', path.resolve(__dirname, 'assets/shop/checkout_complete_entrypoint.js'))
      .addEntry('commerce-weavers-sylius-tpay-shop-order-show-entry', path.resolve(__dirname, 'assets/shop/order_show_entrypoint.js'))
      .disableSingleRuntimeChunk()
      .cleanupOutputBeforeBuild()
      .enableSourceMaps(!Encore.isProduction())
      .enableVersioning(Encore.isProduction())
      .enableSassLoader((options) => {
        // eslint-disable-next-line no-param-reassign
        options.additionalData = `$rootDir: ${rootDir};`;
      })

    const shopConfig = Encore.getWebpackConfig();

    shopConfig.name = 'commerce_weavers_sylius_tpay_shop';

    Encore.reset();

    return shopConfig;
  }

  static getWebpackAdminConfig(rootDir) {
    Encore
      .setOutputPath('public/build/commerce-weavers/tpay/admin')
      .setPublicPath('/build/commerce-weavers/tpay/admin')
      .addEntry('commerce-weavers-sylius-tpay-admin-payment-method-entry', path.resolve(__dirname, 'assets/admin/payment_method_entrypoint.js'))
      .disableSingleRuntimeChunk()
      .cleanupOutputBeforeBuild()
      .enableSourceMaps(!Encore.isProduction())
      .enableVersioning(Encore.isProduction())
      .enableSassLoader((options) => {
        // eslint-disable-next-line no-param-reassign
        options.additionalData = `$rootDir: ${rootDir};`;
      })

    const adminConfig = Encore.getWebpackConfig();

    adminConfig.name = 'commerce_weavers_sylius_tpay_admin';

    Encore.reset();

    return adminConfig;
  }
}

module.exports = CommerceWeaversSyliusTpay;
