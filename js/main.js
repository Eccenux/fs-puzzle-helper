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
	document.querySelector('#toggle-hide-done').addEventListener('click', ()=>{
		document.body.classList.toggle('hide-done');
	});
	document.querySelector('#toggle-hide-passcode').addEventListener('click', ()=>{
		document.body.classList.toggle('hide-passcode');
	});

	// show/hide buttons (generic widget)
	document.querySelectorAll('.show-hide-button').forEach((el)=>{
		el.addEventListener('click',()=>{
			el.classList.toggle('shown');
			el.classList.toggle('hidden');
		});
	});

	// experitmental
	passcodeInit();

	// load first cell
	$('#cell_col_001_001').click();
	
	// @deprecated
	// hide passcode if no fields were field
	let codeFields = [...document.querySelectorAll('.passcode-col-char input')].filter(el=>el.value.length);
	if (codeFields.length < 1 && location.search.indexOf('gsurl=') < 0) {
		$('#toggle-hide-passcode').click();
	}
});

// expose app
const app = {
	columnsViewModel,
	portalsViewModel,
	zoomerViewModel,
};
window.app = app;

/**
 * Quick passcode controls
 * @deprecated
 */
function passcodeInit () {
	const mainContainer = document.querySelector('#passcode-container');
	const container = document.querySelector('#sheet-size-controls');
	if (!container) {
		return;
	}
	const resizers = container.querySelectorAll('.resize');
	const classes = [...resizers].map(el=>el.getAttribute('data-class'));
	resizers.forEach(button => {
		button.addEventListener('click', ()=>{
			let resizeClass = button.getAttribute('data-class');
			mainContainer.classList.remove(...classes);
			mainContainer.classList.add(resizeClass);
		});
	})
}