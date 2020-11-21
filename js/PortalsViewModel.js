import {PortalsState} from './PortalsState.js';
import {PortalCell} from './PortalCell.js';

/**
 * Portals view handler.
 */
class PortalsViewModel {
	constructor() {
		/**
		 * State manager.
		 */
		this.state = new PortalsState();
	}
	/**
	 * Init interactions.
	 * @param {String} PortalsSelector CSS selector for all Portals.
	 */
	init() {
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
				let done = img.classList.toggle('done-cell');
				// async save
				setTimeout(() => {
					if (done) {
						let cell = PortalCell.fromImage(img);
						this.state.setDone(cell);
					} else {
						this.state.setUnDone(img.id);
					}
				});
			});
		});
	}

	/**
	 * State loader.
	 */
	load() {
		this.state.init();
		let doneCells = this.state.getDoneIds();
		if (doneCells.length) {
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
				this.state.resetAll();
				document.querySelectorAll('.done-cell')
					.forEach(el=>{el.classList.remove('done-cell')})
				;
			}
		});
	}
}

export { PortalsViewModel }