import {ColumnsState} from './ColumnsState.js';

/**
 * Columns view handler.
 */
class ColumnsViewModel {
	constructor() {
		/**
		 * State manager.
		 */
		this.state = new ColumnsState();
	}
	/**
	 * Init interactions.
	 * @param {String} columnsSelector CSS selector for all columns.
	 */
	init() {
		this.state.init('.column');
		this.initZoomer();
		this.initDoneToggle();
		this.initReset();
	}

	/**
	 * Enlarge col.
	 */
	initZoomer() {
		let zoomer = document.querySelector('#zoomer img');
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('click', ()=>{
				zoomer.src = img.src;
				// mark active (current)
				document.querySelectorAll('#columns .column.active').forEach((prev)=>{
					prev.classList.remove('active');
				})
				img.parentNode.classList.add('active');
			});
		});	
	}

	/**
	 * Show/hide col.
	 */
	initDoneToggle() {
		document.querySelectorAll('.column h2').forEach(head=>{
			head.addEventListener('click', ()=>{
				this.state.toggleDone(head.parentNode);
			});
		});
	}

	/**
	 * Reset all.
	 */
	initReset() {
		document.querySelector('#reset-all').addEventListener('click', ()=>{
			let confirmed = confirm('Reset all columns?');
			if (confirmed) {
				this.state.resetAll();
			}
		});
	}
}

export { ColumnsViewModel }