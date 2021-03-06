// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart1);

// Draw the chart and set the chart values
function drawChart1() {
	
	var ourRequest = new XMLHttpRequest();//tworzymy obiekt klasy XMLHttpRequest
	url = '/balance/jsonEncode';
	
	ourRequest.open('GET',url, false);
	
	ourRequest.onload = function(){
		if(ourRequest.status >= 200 && ourRequest.status < 400){
			
			var ourData = JSON.parse(ourRequest.responseText);		
			
			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Kategoria');
			data.addColumn('number', 'Kwota');
			
			for (let i=0; i<ourData.length; i++){
				data.addRows([
				[ourData[i].name, parseInt(ourData[i].amountSum)]
				]);
			};

			var options = {
				'width':420, 
				'height':300,
				backgroundColor: 'white',
				legend: {alignment:'center' , textStyle: {color: 'black', fontSize: 14}},
				chartArea:{width:'100%',height:'100%'},
				pieSliceBorderColor: 'grey',
				sliceVisibilityThreshold: .05,
				pieSliceTextStyle: {color:'white', fontSize: 14}
			};

			var chart = new google.visualization.PieChart(document.getElementById('piechart'));
			chart.draw(data, options);
		
		} 
		else {
			console.log("Server return error");
		}
	};

	ourRequest.onerror = function() {  
		console.log("Connection error");
	};
	
	ourRequest.send();
}
