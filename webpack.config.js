var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('./public/')
    .setPublicPath('')
    .setManifestKeyPrefix('')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(false)
    .enableVersioning(true)
    .disableSingleRuntimeChunk()

    .configureCssMinimizerPlugin((options) => {
        options.minimizerOptions = {
            preset: [
                'default',
                {
                    // disabled to fix these issues: https://github.com/EasyCorp/EasyAdminBundle/pull/5171
                    svgo: false,
                },
            ]
        };
    })

    .addEntry('app', './assets/js/app.js')
    .addEntry('form', './assets/js/form.js')
    .addEntry('page-layout', './assets/js/page-layout.js')
    .addEntry('page-color-scheme', './assets/js/page-color-scheme.js')
    .addEntry('field-boolean', './assets/js/field-boolean.js')
    .addEntry('field-code-editor', './assets/js/field-code-editor.js')
    .addEntry('field-collection', './assets/js/field-collection.js')
    .addEntry('field-file-upload', './assets/js/field-file-upload.js')
    .addEntry('field-image', './assets/js/field-image.js')
    .addEntry('field-slug', './assets/js/field-slug.js')
    .addEntry('field-textarea', './assets/js/field-textarea.js')
    .addEntry('field-text-editor', './assets/js/field-text-editor.js')
    .addEntry('login', './assets/js/login.js')
;

module.exports = Encore.getWebpackConfig();
