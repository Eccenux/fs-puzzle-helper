//import {PortalsState} from './PortalsState.js';
import {StateStore} from './StateStore.js';

/**
 * Portals view handler.
 */
class PortalsViewModel {
	constructor() {
		/**
		 * State manager.
		 */
		//this.state = new PortalsState();
		this.store = new StateStore();
	}
	/**
	 * Init interactions.
	 * @param {String} PortalsSelector CSS selector for all Portals.
	 */
	init() {
		//this.state.init('.column');
		this.load();
		this.initDoneMarks();
		this.initReset();
	}

	/**
	 * Adding done-marks.
	 */
	initDoneMarks() {
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('dblclick', ()=>{
				img.classList.toggle('done-cell');
				// store state
				let doneCells = [...document.querySelectorAll('.done-cell')].map(el=>el.id);
				this.store.write(doneCells);
			});
		});
	}

	/**
	 * Simplified state loader.
	 */
	load() {
		let doneCells = this.store.read();
		if (doneCells && Array.isArray(doneCells)) {
			doneCells
				.map(id=>document.getElementById(id))
				.forEach(el=>{el.classList.add('done-cell')})
			;
		}
	}

	/**
	 * Reset all.
	 */
	initReset() {
		document.querySelector('#reset-all').addEventListener('click', ()=>{
			let confirmed = confirm('Reset all portals?');
			if (confirmed) {
				this.store.write(null);
				document.querySelectorAll('.done-cell')
					.forEach(el=>{el.classList.remove('done-cell')})
				;
			}
		});
	}
}

export { PortalsViewModel }