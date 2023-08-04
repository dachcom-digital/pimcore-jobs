document.addEventListener(pimcore.events.pimcoreReady, (e) => {

    let user = pimcore.globalmanager.get('user'),
        jobsMenu,
        openSettings = function (config) {
            try {
                pimcore.globalmanager.get('jobs_bundle_settings').activate();
            } catch (e) {
                pimcore.globalmanager.add('jobs_bundle_settings', new Jobs.SettingsPanel(config));
            }
        };

    if (!user.isAllowed('jobs_permission_settings')) {
        return false;
    }

    jobsMenu = new Ext.Action({
        id: 'jobs_bundle_setting_button',
        text: t('jobs.settings.configuration'),
        iconCls: 'jobs_icon_bundle',
        handler: openSettings.bind(this)
    });

    if (layoutToolbar.settingsMenu) {
        layoutToolbar.settingsMenu.add(jobsMenu);
    }
});
