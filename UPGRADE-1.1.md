# UPGRADE FROM `1.0` TO `1.1`

1. The `cw.tpay.admin.payment_method.form` template event has been added to the
   `templates/bundles/SyliusAdminBundle/PaymentMethod/_form.html.twig` template. Apply the following customization to 
   your project:

```diff
{# templates/bundles/SyliusAdminBundle/PaymentMethod/_form.html.twig #}

{# ... #}

<div class="ui segment">
    <h4 class="ui dividing header">{{ 'sylius.ui.gateway_configuration'|trans }}</h4>

    {# ... #}

+    {# >>> SyliusTpayPlugin customization #}
+    {{ sylius_template_event('cw.tpay.admin.payment_method.form', _context) }}
+    {# SyliusTpayPlugin customization <<< #}
</div>

{# ... #}
```

1. Add an admin Webpack config to your `webpack.config.js` file:

```diff
// webpack.config.js

// ...

const cwTpayShop = CommerceWeaversSyliusTpay.getWebpackShopConfig(path.resolve(__dirname));
+const cwTpayAdmin = CommerceWeaversSyliusTpay.getWebpackAdminConfig(path.resolve(__dirname));

-module.exports = [shopConfig, adminConfig, cwTpayShop];
+module.exports = [shopConfig, adminConfig, cwTpayShop, cwTpayAdmin];
```
