/**
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2013 breakDesigns.net. All rights reserved
 */

window.addEvent('domready', function() {
	let displayTypesDropDowns = document.querySelectorAll('.cfDisplayTypes');

	displayTypesDropDowns.forEach(function (displayTypesDropDown){
		displayTypesDropDown.addEventListener('change', function() {
			let dropdown_id = this.getAttribute('id');
			let filterid = dropdown_id.substring(7);
			showSettingsByDisplayType(filterid);
		});
	});

	if (document.getElementById('cfOptimizerForm') != null) {
		document.getElementById('cfOptimizerForm').addEventListener('submit', function(e) {
			e.preventDefault();
			let target=document.getElementById('optimizer_results');
			var req = new Request.JSON({
				method : 'post',
				onRequest : function() {
					target.classList.add('cf_spinner');

				},
				onSuccess : function(response) {
					setInterval(function(){displayResults(response,target);},2000);
					
				}
			});
			req.post(this);
		});
	}
	
	/**
	 * Display the results of the optimizer
	 * @since 1.9.5
	 * @author Sakis Terz
	 */
	function displayResults(response,target){
		let found=response.found.length;
		let notFound=response.Notfound.length;
		let added=response.added.length;
		let success=response.success;
		let html='<div class="cf_log_wrapper">';
		if (success!=-1){
			html+='<span class="msg_neutral">Indexes Found: '+found+'</span><br/>';
			html+='<span class="msg_division">'+notFound+'/'+added+' Indexes Added.</span> <br/><span class="msg_precentage">Success'+parseInt(success)*100+'%</span>';
		}else {
			html+='<span class="msg_neutral">No missing indexes found. No action required</span>';
		}
		html+='</div>';
		target.classList.remove('cf_spinner');
		target.innerHTML=html;
	}
})