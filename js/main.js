/**
 * Main entry point.
 */
import {ColumnsViewModel} from './ColumnsViewModel.js';
import {PortalsViewModel} from './PortalsViewModel.js';
import {ZoomerViewModel} from './ZoomerViewModel.js';

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