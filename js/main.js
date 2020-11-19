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

	document.querySelectorAll('.column img').forEach(img=>{
		img.addEventListener('dblclick', ()=>{
			img.classList.toggle('done-cell');
		});
	});

	document.querySelector('#toggle-view-all').addEventListener('click', ()=>{
		document.body.classList.toggle('view-all');
	});
});

/**
// Save portals state (simple on/off state)
doneCells = [...document.querySelectorAll('.done-cell')]
	.map(el=>el.id)
;
localStorage.setItem('temp-portal-done', JSON.stringify(doneCells));

// Load portals stare
doneCells = JSON.parse(localStorage.getItem('temp-portal-done'));
doneCells
	.map(id=>document.getElementById(id))
	.forEach(el=>{el.classList.add('done-cell')})
;
*/