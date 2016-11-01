document.getElementById('panel').addEventListener('click', (function (e){
	let show=false;
	return function (e){
		if (e.target.tagName === 'DIV') return false;
		if (document.getElementById('monitor').classList.contains('proc')) return false;
		if (e.target.innerHTML === '=') {
			let number=document.getElementById('monitor').innerHTML;
			socket.send(JSON.stringify(number));
			show = true;
			return false;
		}
		if (show) {
			document.getElementById('monitor').innerHTML = '0';
			show = !show;
		}
		if (document.getElementById('monitor').innerHTML.length>15) return false;
		if (e.target.innerHTML === '.') {
		if (document.getElementById('monitor').innerHTML.indexOf('.') === -1) 
			document.getElementById('monitor').innerHTML += '.';	
			return false;
		}
		if (document.getElementById('monitor').innerHTML == '0') 
			document.getElementById('monitor').innerHTML = e.target.innerHTML;
		else document.getElementById('monitor').innerHTML += e.target.innerHTML;
		show=false;
	};
})());

window.onload = function(){
	let url = "ws://localhost:8081"; 	
	socket = new WebSocket(url); 
	socket.onopen = function() {
		document.getElementById('calculate').disabled = false;
		document.getElementById('monitor').classList.remove('proc');
		document.getElementById('calculate').classList.add('number');
		document.getElementById('monitor').innerHTML = '0';
	}
	socket.onmessage = function(e) {
		var sqrNumber = JSON.parse(e.data);
		document.getElementById('monitor').innerHTML = sqrNumber;
		if (document.getElementById('monitor').innerHTML.length>15 ) 
			document.getElementById('monitor').innerHTML = 'Too long';
	};
	socket.onerror = function(){
		document.getElementById('monitor').innerHTML = 'Error';
		document.getElementById('calculate').disabled=true;
		document.getElementById('monitor').classList.add('proc');
		document.getElementById('calculate').classList.remove('number');
	}; 
	socket.onclose = function(){
		document.getElementById('monitor').innerHTML = 'Closed';
		document.getElementById('calculate').disabled=true;
		document.getElementById('monitor').classList.add('proc');
		document.getElementById('calculate').classList.remove('number');
	}; 
};







