function sendAmountAndCategory() {
	
	let amount = document.getElementById('amountOfMoney').value;
	let category = document.getElementById('category').value;
	
	let dateString = document.getElementById('currentDate').value;
	let date = new Date(dateString);
	
	let n =  new Date();
	let currentMonth = n => n.getMonth() + 1;
	let nextMonth = n => n.getMonth() + 2;
	let currentYear = n => n.getFullYear();
	
	let cm = currentMonth(n);
	let nm;
	let cy;
	let ny;
	if(cm == 12) {	
		nm = '01';
		cy = currentYear(n);
		ny = cy + 1;
	}
	else {
		nm = nextMonth(n);
		cy = currentYear(n);
		ny = currentYear(n);
	}

	let currentMonthStart = new Date(cy + '-' + cm + '-01');
	let currentMonthEnd = new Date(ny + '-' + nm + '-01');
	
	let categoryName;
	let limit;

	if(date >= currentMonthStart && date < currentMonthEnd) {	
	fetch("/expense/getLimitOfExpense")
			
	// Converting received data to JSON
		.then(response => response.json())
		.then(json => {
		   
			// Create a variable to store HTML
			let div0 = '<div class="col-12" style="font-size:18px; font-weight: bold;">Zestawienie dla bieżącego miesiąca</div>';
			let div1 = '<div class="col-8">Limit dla kategorii:</div>';
				
			// Loop through each data and add a table row
			json.forEach(expense => {
				
				if((`${expense.id}` == category) && (`${expense.limitExpense}` !== 'null')) {
					div1 += `<div style="text-align:right;" class="col-4">${expense.limitExpense} zł</div>`;
					categoryName = `${expense.name}`;
					limit = `${expense.limitExpense}`;
				}
			});

			fetch("/expense/getAmountOfExpenseThisMonth")
			
			// Converting received data to JSON
			.then(response => response.json())
			.then(json => {
		   
				// Create a variable to store HTML
				let div2 = '<div class="col-8">Dotychczasowe wydatki:</div>';
				let div3 = '<div class="col-8">Wydatki z uwzględnieniem kwoty:</div>';
				let div4;
				let amountSum = 0;
				
				// Loop through each data and add a table row
				json.forEach(expense => {
					if(`${expense.name}` == categoryName){
						amountSum = `${expense.amountSum}`;
					}
				});	
					if(amountSum == 0)
					div2 += '<div style="text-align:right;" class="col-4">'+amountSum+'.00 zł</div>';				
					else
					div2 += '<div style="text-align:right;" class="col-4">'+amountSum+' zł</div>';
							
					let amountTotal = Math.round((parseFloat(amount) + parseFloat(amountSum))*100)/100;
					let amountTotalRound = Math.round(amountTotal);
												
					if(amountTotal == amountTotalRound) {amountTotal = amountTotal + '.00';}
					div3 += '<div style="text-align:right;" class="col-4">'+ amountTotal +' zł</div>';
							
					let difference = Math.round((parseFloat(limit) - amountTotal)*100)/100;
					let differenceRound = Math.round(difference);
						
					if(difference >= 0) {
						if(difference == differenceRound) {difference = difference + '.00';}
						div4 = '<div class="col-8">Do przekroczenia limitu pozostało:</div>';
						div4 += '<div style="text-align:right;" class="col-4">'+ difference +' zł</div>';
					}
						
					else {
						difference = -difference;
						differenceRound = -differenceRound;
						if(difference == differenceRound) {difference = difference + '.00';}
						div4 = '<div style="color: red;" class="col-8">Limit zostanie przekroczony o:</div>';
						div4 += '<div style="text-align:right; color: red;" class="col-4">'+ difference +' zł</div>';						
					}
	

				if(limit > 0) {
					document.getElementById("limit0").innerHTML = div0;
					document.getElementById("limit1").innerHTML = div1;
					document.getElementById("limit2").innerHTML = div2;
					document.getElementById("limit3").innerHTML = div3;
					document.getElementById("limit4").innerHTML = div4;
				}
				else {
					document.getElementById("limit0").innerHTML = '';
					document.getElementById("limit1").innerHTML = '';
					document.getElementById("limit2").innerHTML = '';
					document.getElementById("limit3").innerHTML = '';
					document.getElementById("limit4").innerHTML = '';					
				}
				
			});	
		});	 
	}
	else { 
		document.getElementById("limit0").innerHTML = '';
		document.getElementById("limit1").innerHTML = '';
		document.getElementById("limit2").innerHTML = '';
		document.getElementById("limit3").innerHTML = '';
		document.getElementById("limit4").innerHTML = '';
	}
}	