pimcore.registerNS('pimcore.plugin.Jobs');

pimcore.plugin.Jobs = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return 'pimcore.plugin.Jobs';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
        // void
    },

    pimcoreReady: function (params, broker) {

        var jobsMenu, user = pimcore.globalmanager.get('user');

        if (!user.isAllowed('jobs_permission_settings')) {
            return false;
        }

        jobsMenu = new Ext.Action({
            id: 'jobs_bundle_setting_button',
            text: t('Jobs Configuration'),
            iconCls: 'jobs_icon_bundle',
            handler: this.openSettingsPanel.bind(this)
        });

        if (layoutToolbar.settingsMenu) {
            layoutToolbar.settingsMenu.add(jobsMenu);
        }
    },

    openSettingsPanel: function () {
        try {
            pimcore.globalmanager.get('jobs_bundle_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('jobs_bundle_settings', new Jobs.SettingsPanel());
        }
    }

});

new pimcore.plugin.Jobs();
