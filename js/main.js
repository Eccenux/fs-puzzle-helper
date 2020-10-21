/**
 * Main entry point.
 */
import {ColumnsViewModel} from './ColumnsViewModel.js';

const columnsViewModel = new ColumnsViewModel();

document.addEventListener("DOMContentLoaded", function() {
	columnsViewModel.init();
});
