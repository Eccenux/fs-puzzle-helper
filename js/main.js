/**
 * Main entry point.
 */
import {ColumnsState} from './ColumnsState.js';

let columnsState = new ColumnsState();

document.addEventListener("DOMContentLoaded", function() {
	columnsState.init('.column');

	// enlarge col
	let zoomer = document.querySelector('#zoomer img');
	document.querySelectorAll('.column img').forEach(img=>{
		img.onclick = ()=>{
			zoomer.src = img.src;
			// mark active (current)
			document.querySelectorAll('#columns .column.active').forEach((prev)=>{
				prev.classList.remove('active');
			})
			img.parentNode.classList.add('active');
		};
	});
	// show/hide col
	document.querySelectorAll('.column h2').forEach(head=>{
		head.onclick = ()=>{
			columnsState.toggleDone(head.parentNode);
		};
		/*
		head.ondblclick = ()=>{
			head.parentNode.classList.remove('done');
		};
		*/
	});
});
