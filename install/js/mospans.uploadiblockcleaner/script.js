window.addEventListener('load', function load(event) {
	window.removeEventListener('load', load, false);
	
	var step = 1,
	action,
	actions = ['iblock_analysis', 'file_analysis', 'file_deleting'];
	
	function changeAction()
	{
		action = actions.shift();
		step = 1;
		changeActionState(action);
		makeStep();
	}
	
	function changeActionState(actionState)
	{
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
			alert('Ошибка!');
			document.querySelectorAll('.mospans-progressbar')[0].style.display = 'none';
			document.querySelectorAll('.mospans-run-clean')[0].style.display = 'block';
			return;
		}
		
		setPercentage(parsedData.percentage);
		
		if (!parsedData.action_complete) {
			// если действие еще не закончено, то выполняем следующий шаг
			step++;
			makeStep();
		} else {
			// если действие закончено, то выводим статус окончания
			changeActionState(action + '-complete');
			if (actions.length > 0) {
				// если еще остались доступные действия, то начинаем выполнение следующего
				changeAction();
			}
		}
	};
	
	var button = document.querySelectorAll('.mospans-run-clean')[0].addEventListener('click', function (event) {
		event.preventDefault();
		setPercentage(0);
		changeAction();
	});
}, false);