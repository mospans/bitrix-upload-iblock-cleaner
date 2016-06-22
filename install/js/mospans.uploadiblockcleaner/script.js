window.addEventListener('load', function load(event) {
	window.removeEventListener('load', load, false);
	
	var step = 1,
	action,
	actions = ['analysis'];
	
	function changeAction()
	{
		action = actions.shift();
		changeActionState(action);
	}
	
	function changeActionState(actionState)
	{
		var actionStates = document.querySelectorAll('.mospans-action-state');
		for (var i = 0; i < actionStates.length; i++) {
			actionStates[i].style.display = 'none';
		}
		document.querySelectorAll('.mospans-action-state_' + actionState.replace('_', '-'))[0].style.display = 'block';
	}
	
	var makeStep = function () {
		BX.ajax.post('/bitrix/admin/mospans_step_cleaner.php', {action: action, step: step}, callbackStepCleaner);
	};
	
	var setPercentage = function (value) {
		document.querySelectorAll('.mospans-progressbar')[0].style.display = 'block';
		document.querySelectorAll('.mospans-progressbar__line')[0].style.width = value.toString() + '%';
		document.querySelectorAll('.mospans-progressbar__content')[0].innerHTML = value.toString() + '%';
	};
	
	var callbackStepCleaner = function (data) {
		document.querySelectorAll('.mospans-run-clean')[0].style.display = 'none';
		
		if (!data) {
			return;
		}
		
		var parsedData = JSON.parse(data);
		if (!('error' in parsedData && !parsedData.error) || !('percentage' in parsedData) || !('action_complete' in parsedData)) {
			alert('Error!');
			document.querySelectorAll('.mospans-progressbar')[0].style.display = 'none';
			document.querySelectorAll('.mospans-run-clean')[0].style.display = 'block';
			return;
		}
		
		setPercentage(parsedData.percentage);
		
		if (!parsedData.action_complete) {
			step++;
			makeStep();
		} else {
			changeActionState(action + '-complete');
		}
	};
	
	var button = document.querySelectorAll('.mospans-run-clean')[0].addEventListener('click', function (event) {
		event.preventDefault();
		setPercentage(0);
		changeAction();
		makeStep();
	});
}, false);