function setStartDate() {
	let n =  new Date();
	let y = n.getFullYear();
	let m = n.getMonth() + 1;
	if(m<10) m = '0'+m;
	d = n.getDate();
	if(d<10) d = '0'+d;
	
	return y + "-" + m + "-" + d
}

document.getElementById("currentDate").value = setStartDate();
