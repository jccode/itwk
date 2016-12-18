/**
 * 选择行业
 * 
 * findIndusById():  defined in keke.js
 * 
 */

(function ($, _, window) {

	var emptyOptionHtml = '<option value="">请选择分类</option>';
	
	function SelectIndus (opt) {
		this.opts = $.extend({}, SelectIndus.defualt);
		if(opt["data"]) {
			$.extend(this.opts, opt);
		} else {
			this.opts.data = opt;
		}
		this.l1indus = _.values(this.opts.data);

		return this;
	}


	SelectIndus.prototype = {

		/**
		 * Initialize the select box states. 
		 * If indus_id & indus_pid is provided, select box checked state will be init as well.
		 *
		 * @param indus_id   (optional)
		 * @param indus_pid  (optional)
		 */
		init: function (indus_id, indus_pid) {
			this._initSelectBoxs();
			if(indus_id && indus_pid) {
				this.initCheckState(indus_id, indus_pid);
			}
		}, 


		_initSelectBoxs: function () {
			var l1optionHtml = _.map(this.l1indus, genOption).join('');
			var self = this;
			self._selectbox1()
				.html(emptyOptionHtml + l1optionHtml)
				.change(function (e) {
					self._l1changeHandler(e);
				});

			self._selectbox2().change(function (e) {
				self._l2changeHandler(e);
			});
		}, 

		initCheckState: function (indus_id, indus_pid) {
			var indusp = findIndusById(indus_pid, this.l1indus);
			
			var indus_ids = indusp.indus_pid != 0 ?  
					[indusp.indus_pid, indus_pid, indus_id] :  	// three level indus_id
						[indus_pid, indus_id]; 				// two level indus_id
			
			this._selectbox1().val(indus_ids[0]).trigger("change");
			this._selectbox2().val(indus_ids[1]).trigger("change");
			if(indus_ids.length > 2) {
				this._selectbox3().val(indus_ids[2]);
			}
		}, 

		_l1changeHandler: function(e) {
			var val = this._selectbox1().val();
			var l2Options = _.find(this.l1indus, function(indus) {
					return indus['indus_id'] == val;
				})["children"];
			var l2OptionHtml = _.map(l2Options, genOption).join('');
			this._selectbox2().html(emptyOptionHtml + l2OptionHtml);
		}, 

		_l2changeHandler: function (e) {
			var id = this._selectbox1().val(), 
				val = this._selectbox2().val();
			var l2indus = _.find(this.l1indus, function(indus) {
				return indus['indus_id'] == id;
			})["children"];

			var l3OptionHtml = emptyOptionHtml;

			if(l2indus) {
				var l3indus = _.find(l2indus, function(indus) {
					return indus['indus_id'] == val;
				})["children"];
				l3OptionHtml += _.map(l3indus, genOption).join('');
			}
			
			this._selectbox3().html(l3OptionHtml);
		}, 

		_selectbox1: function () {
			return $("#" + this.opts.select_id_level_1);
		}, 

		_selectbox2: function () {
			return $("#" + this.opts.select_id_level_2);
		}, 

		_selectbox3: function () {
			return $("#" + this.opts.select_id_level_3);
		}
	};

	SelectIndus.defualt = {
		"select_id_level_1": "selectp", 
		"select_id_level_2": "indus_pid", 
		"select_id_level_3": "indus_id"
	}


	function genOption (indus) {
		return '<option value="' + indus['indus_id'] + '" data-pid="' + indus['indus_pid'] + '">' 
			+ indus['indus_name'] + '</option>';
	}



	window.SelectIndus = SelectIndus;

})(jQuery, _, window);

