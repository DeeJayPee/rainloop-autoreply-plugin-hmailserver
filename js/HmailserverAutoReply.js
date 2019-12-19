
(function () {

	/**
	 * @constructor
	 */
	function HmailserverAutoReply()
	{
		this.vacationmessageon = ko.observable('');
		this.vacationsubject = ko.observable('');
		this.vacationmessage = ko.observable('');
		this.vacationmessageexpires = ko.observable('');
		this.vacationmessageexpiresdate = ko.observable('');

		this.loading = ko.observable(false);
		this.saving = ko.observable(false);

		this.savingOrLoading = ko.computed(function () {
			return this.loading() || this.saving();
		}, this);
	}

	HmailserverAutoReply.prototype.customAjaxSaveData = function ()
	{
		var self = this;

		if (this.saving())
		{
			return false;
		}

		this.saving(true);

		window.rl.pluginRemoteRequest(function (sResult, oData) {

			self.saving(false);

			if (window.rl.Enums.StorageResultType.Success === sResult && oData && oData.Result)
			{
				// true
			}
			else
			{
				// false
			}

		}, 'AjaxSaveCustomUserData', {
			'vacationmessageon': this.vacationmessageon(),
			'vacationsubject': this.vacationsubject(),
			'vacationmessage': this.vacationmessage(),
			'vacationmessageexpires': this.vacationmessageexpires(),
			'vacationmessageexpiresdate': this.vacationmessageexpiresdate()
		});
	};

	HmailserverAutoReply.prototype.onBuild = function () // special function
	{
		var self = this;

		this.loading(true);

		window.rl.pluginRemoteRequest(function (sResult, oData) {

			self.loading(false);

			if (window.rl.Enums.StorageResultType.Success === sResult && oData && oData.Result)
			{
				self.vacationmessageon(oData.Result.vacationmessageon || '');
				self.vacationsubject(oData.Result.vacationsubject || '');
				self.vacationmessage(oData.Result.vacationmessage || '');
				self.vacationmessageexpires(oData.Result.vacationmessageexpires || '');
				self.vacationmessageexpiresdate(oData.Result.vacationmessageexpiresdate || '');
			}

		}, 'AjaxGetCustomUserData');

	};

	window.rl.addSettingsViewModel(HmailserverAutoReply, 'PluginHmailserverAutoReplyTab',
		'SETTINGS_CUSTOM_PLUGIN/TAB_NAME', 'custom');

}());