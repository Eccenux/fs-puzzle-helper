/**
 * Main entry point.
 */
import {ColumnsViewModel} from './ColumnsViewModel.js';
import {PortalsViewModel} from './PortalsViewModel.js';

const columnsViewModel = new ColumnsViewModel();
const portalsViewModel = new PortalsViewModel();

document.addEventListener("DOMContentLoaded", function() {
	columnsViewModel.init();
	portalsViewModel.init();
});
