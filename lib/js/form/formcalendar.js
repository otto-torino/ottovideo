/*
	javascript functions formcalendar class
	
	callbackCloseCalendar is the function to callback when calendar is closed, init value is null: no function is called
*/

var calendarLayerID = 'calendarLayer';
var calendarLayer;
var post_url = 'methodPointer.php?pt[formcalendar-printCalendar]';
var callbackCloseCalendar = null;


document.onclick=check;

function check(e) {
	
	if($(calendarLayerID)) {
		var calendarLayerName = $(calendarLayerID).getProperty('name');
		var input = calendarLayerName.slice(9)
		var target = (e && e.target) || (event && event.srcElement);
		var calendar = $(calendarLayerID);
		var cal_button = document.getElementById('cal_button_'+input);
		var form_field = document.getElementById(input);
		if(target==cal_button || target==form_field) {
		}
		else {
			checkParent(target)?closeCalendar():null;
		}
		
	}
}

function checkParent(t){
	while(t.parentNode){
		if(t==document.getElementById(calendarLayerID)){
			return false
		}
		t=t.parentNode
	}
	return true
} 	


function printCalendar(openCalendar, inputField) {
	
	if($(calendarLayerID)) $(calendarLayerID).dispose();
	
	calendarLayer = new Element('div', {id:calendarLayerID});
	calendarLayer.setProperty('class', 'calendar');
	calendarLayer.setProperty('name', 'calLayer_'+inputField.getProperty('id'));
	calendarLayer.setStyles({
		'position': 'absolute',
		'width': '0px',
		'height': '0px',
		'overflow': 'hidden',
		'z-index': 1000
	});
	
	calendarLayer.inject($(openCalendar), 'after');	
	
	var myEffect = new Fx.Morph(calendarLayerID, {duration: 'normal', transition: Fx.Transitions.Sine.easeOut});
	
	var req = new Request({
		url:post_url,
		method: 'post',
		data:'input_field='+inputField.getProperty('id'),
		onComplete: function(responseText) {
			calendarLayer.innerHTML = responseText;
			myEffect.start({
    			'height': [0, 170],
    			'width': [0, 212]  
			}).chain(function() {
				$(calendarLayerID).setStyle('height',$(calendarLayerID).getChildren()[0].getStyle('height').toInt()+12);
				$(calendarLayerID).setStyle('overflow','visible');
			});
		}
	}).send();
	
}

function closeCalendar() {
	
	$(calendarLayerID).dispose();
	
}

function fillInputField(date, inputFieldID) {

	$(inputFieldID).setProperty('value', date);
	closeCalendar();
	if(callbackCloseCalendar) callbackCloseCalendar(inputFieldID);

}
