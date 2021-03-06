/* {[The file is published on the basis of YetiForce Public License 2.0 that can be found in the following directory: licenses/License.html or yetiforce.com]} */
Settings_Vtiger_EditModal_Js('Settings_PBX_EditModal_Js', {}, {
	registerEvents: function () {
		this._super();
		var container = this.getForm();
		container.find('[name="type"]').on('change', function (e) {
			if (this.value) {
				AppConnector.request({
					module: app.getModuleName(),
					parent: 'Settings',
					view: 'EditModal',
					type: this.value,
					connectorConfig: true
				}).then(function (html) {
					container.find('.formGroups').append($(html).find('.editModalContent').html());
				});
			}
		});
	}
});
