document.getElementById('panel').addEventListener('click', function (e){
	if (e.target.tagName === 'DIV') return false;
	if (e.target.innerHTML === '=') {
		let number=document.getElementById('monitor').innerHTML;
		socket.send(JSON.stringify(number));
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
});

window.onload = function(){
	let url = "ws://localhost:8081"; 	
	socket = new WebSocket(url); 
	socket.onopen = function() {
		console.log('Connection..');
		document.getElementById('calculate').disabled=false;
	}
	socket.onmessage = function(e) {
		var sqrNumber = JSON.parse(e.data);
		document.getElementById('monitor').innerHTML = sqrNumber;
	};
	socket.onerror = function(){
		console.log('Error with connection');
		document.getElementById('calculate').disabled=true;
	}; 
	socket.onclose = function(){
		console.log('Closed connection');
		document.getElementById('calculate').disabled=true;
	}; 
};







