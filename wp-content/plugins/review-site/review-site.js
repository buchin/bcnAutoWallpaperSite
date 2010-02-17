function rating(num, setnum) {

	var s = num.id.replace(setnum + "_", '');
	
	for (i = 1; i <= 5; i++ ){		
		if (i <= s) {
			document.getElementById(setnum + "_" + i).className = "on";
		} else {
			document.getElementById(setnum + "_" + i).className = "";
		}
	}
	
}

function rolloff(me, setnum) {

	var current = document.getElementById(setnum + "_rating").value;
	

	for (i = 1; i <= 5; i++) {
		if (i <= current) {
			document.getElementById(setnum + "_" + i).className = "on";
		} else {
			document.getElementById(setnum + "_" + i).className = "";
		}
	}

}

function rateIt(me, setnum){

	var s = me.id.replace(setnum + "_", '');
	document.getElementById(setnum + "_rating").value = s;	
	rolloff(me, setnum);

}