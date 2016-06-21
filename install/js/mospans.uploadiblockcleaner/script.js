window.addEventListener('load', function load(event) {
	window.removeEventListener('load', load, false);
	
	var step = 0;
	var makeStep = function () {
		BX.ajax.post('/bitrix/admin/mospans_step_cleaner.php', {step: step}, callbackStepCleaner);
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
		if (!('error' in parsedData && !parsedData.error) || !('percentage' in parsedData)) {
			alert('Error!');
			return;
		}
		
		setPercentage(parsedData.percentage);
		if (parsedData.percentage < 100) {
			step++;
			makeStep();
		}
	};
	
	var button = document.querySelectorAll('.mospans-run-clean')[0].addEventListener('click', function (event) {
		event.preventDefault();
		setPercentage(0);
		makeStep();
	});
}, false);