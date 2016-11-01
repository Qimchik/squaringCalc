function onclick(e){
	if (e.target.tagName === 'DIV') return false;
	if (e.target.innerHTML === '=') {
		calculate();
		return false;
	}
	if (e.target.innerHTML === '.') {
	if (document.getElementById('monitor').innerHTML.indexOf('.') === -1) 
		document.getElementById('monitor').innerHTML += '.';	
		return false;
	}
	if (document.getElementById('monitor').innerHTML == '0') 
		document.getElementById('monitor').innerHTML = e.target.innerHTML;
	else document.getElementById('monitor').innerHTML += e.target.innerHTML;
}










document.getElementById('panel').addEventListener('click', onclick);