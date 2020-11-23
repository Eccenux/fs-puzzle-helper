/**
 * Main entry point.
 */
import {ColumnsViewModel} from './ColumnsViewModel.js';
import {PortalsViewModel} from './PortalsViewModel.js';
import {ZoomerViewModel} from './ZoomerViewModel.js';
import {StateStore} from './StateStore.js';

const columnsViewModel = new ColumnsViewModel();
const portalsViewModel = new PortalsViewModel();
const zoomerViewModel = new ZoomerViewModel();

// init on load
document.addEventListener("DOMContentLoaded", function() {
	columnsViewModel.init();
	portalsViewModel.init();
	zoomerViewModel.init(portalsViewModel);

	document.querySelector('#toggle-view-all').addEventListener('click', ()=>{
		document.body.classList.toggle('view-all');
	});

	//
	// quick passcode state
	let storage = new StateStore('puzzle-chars-state');
	let inputs = document.querySelectorAll('#passcode-columns .passcode-col-char input');
	// load
	let oldValues = storage.read();
	if (oldValues && oldValues.length) {
		console.log('oldValues:', oldValues);
		oldValues.forEach((state)=>{
			let column = state.col;
			let value = state.val;
			let input = document.querySelectorAll(`#passcode-columns .passcode-col-char [data-col='${column}']`);
			input.value = value;
			//console.log(input, state)
		});
	}
	// save
	inputs.forEach((input)=>{
		input.addEventListener('change', function() {
			let values = [...inputs].map(el=>{
				return {
					val:el.value,
					col:el.getAttribute('data-col'),
				};
			});
			console.log('storing:', values);
			storage.write(values);
		});
	});

	// load first cell
	$('#cell_col_001_001').click();
});

// expose app
const app = {
	columnsViewModel,
	portalsViewModel,
	zoomerViewModel,
};
window.app = app;