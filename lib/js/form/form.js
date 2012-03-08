function confirmSubmit() {
	var agree=confirm("Sicuro di voler procedere?");
	if (agree)
		return true ;
	else
		return false ;
}

function ValidateForm(formID){
	
	var labels = $$('#'+formID+' label');
	var validationOK=true; 

	for(var i=0;i<labels.length;i++)
	{
		var lab=labels[i];
		if(lab.className=="req2") lab.className="req";
		if(lab.className=="req")
		{
			var form_element_id = lab.getProperty('for');
			var match_sb = /(.*?)\[\]/.exec(form_element_id);
			if(match_sb && match_sb.length>0) 
				var form_elements = $$('#'+formID+' [name^='+match_sb[1]+'[]');
			else 
				var form_elements = $$('#'+formID+' [name='+form_element_id+']');
			var form_element = form_elements[0];
			
			if(form_element.match('input')) {
				if(form_element.getProperty('type')=='text') {
					if(form_element.getProperty('value') == '' || (form_element_id=='email' && !isEmail(form_element.getProperty('value')))) {
						lab.className="req2";   //input vuoto o email non valida
						validationOK=false;
					}
				}
				else if(form_element.getProperty('type')=='checkbox') {
					var checked = false;
					for(var l=0;l<form_elements.length;l++) {
						if(form_elements[l].checked) checked = true;
					}
				       	if(!checked) {
						lab.className="req2";   //checkbox non selezionato
						validationOK=false;
					}
				}
				else if(form_element.getProperty('type')=='radio') {
					var checked = false;
					for(var iii=0;iii<form_elements.length;iii++) {
						if(form_elements[iii].checked) checked = true;
					}
				       	if(!checked) {
						lab.className="req2";   //radio non selezionato
						validationOK=false;
					}
				}
				else if(form_element.getProperty('type')=='hidden') {
					if(FCKeditorAPI.GetInstance(form_element_id).GetXHTML() == "") {
						lab.className="req2";   //input vuoto
						validationOK=false;
					}
				}
				else if(form_element.getProperty('type')=='file') {
					if(form_element.getProperty('value') == '') {
						lab.className="req2";   //input vuoto 
						validationOK=false;
					}
				}
				else if(form_element.getProperty('type')=='password') {
					if(form_element.getProperty('value') == '') {
						lab.className="req2";   //input vuoto 
						validationOK=false;
					}
				}

			}
			else if(form_element.match('textarea')) {
				if(form_element.getProperty('value') == '') {
					lab.className="req2";   //textarea vuoto
					validationOK=false;
				}
			}
			else if(form_element.match('select')) { 
				if(form_element.getProperty('value') == '') {
					lab.className="req2";   //select vuoto
					validationOK=false;
				}
			}
		}
	}
	if(!validationOK) {
		alert("Attenzione: alcuni campi obbligatori non sono stati inseriti");
		return false;
	}
	else return true;
}

function isEmail(str){
	var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
	var r2 = new RegExp("^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,3}|[0-9]{1,3})(\\]?)$");
	return (!r1.test(str) && r2.test(str));
}
