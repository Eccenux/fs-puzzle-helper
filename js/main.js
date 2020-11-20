/**
 * Main entry point.
 */
import {ColumnsViewModel} from './ColumnsViewModel.js';
import {PortalsViewModel} from './PortalsViewModel.js';
import {ZoomerViewModel} from './ZoomerViewModel.js';

const columnsViewModel = new ColumnsViewModel();
const portalsViewModel = new PortalsViewModel();
const zoomerViewModel = new ZoomerViewModel();

document.addEventListener("DOMContentLoaded", function() {
	columnsViewModel.init();
	portalsViewModel.init();
	zoomerViewModel.init();

	document.querySelector('#toggle-view-all').addEventListener('click', ()=>{
		document.body.classList.toggle('view-all');
	});
});
