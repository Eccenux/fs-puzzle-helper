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
	 * Change done state (both in DOM and storage).
	 * 
	 * Note! This is async.
	 * 
	 * @param {Element} img Cell-image.
	 * @param {boolean} done Done / not done.
	 * @param {boolean} skipSave Skip save (use only if state was already stored).
	 */
	changeDoneState(img, done, skipSave) {
		if (done) {
			img.classList.add('done-cell');
		} else {
			img.classList.remove('done-cell');
		}
		if (!skipSave) {
			this.saveDoneState(img, done);
		}
	}

	/**
	 * Apply portal changes.
	 * 
	 * Note! This is async.
	 * 
	 * @param {Portal} portal Modified portal data.
	 */
	changePortalState(portal) {
		// async save
		setTimeout(() => {
			this.state.setPortal(portal);
		});
	}

	/**
	 * Adding done-marks.
	 * @private
	 */
	initDoneMarks() {
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('dblclick', ()=>{
				let done = img.classList.toggle('done-cell');
				this.saveDoneState(img, done);
			});
		});
	}

	/**
	 * Save done state for cell.
	 * 
	 * Note! This is async.
	 * @private
	 * @param {Element} img Cell-image.
	 * @param {boolean} done Done / not done.
	 */
	saveDoneState(img, done) {
		// async save
		setTimeout(() => {
			if (done) {
				let cell = PortalCell.fromImage(img);
				this.state.setDone(cell);
			} else {
				this.state.setUnDone(img.id);
			}
		});
	}

	/**
	 * Get portal model.
	 * @param {Element} img Cell-image.
	 */
	getPortal(img) {
		let cell = PortalCell.fromImage(img);
		let portal = this.state.getPortal(cell);
		return portal;
	}

	/**
	 * State loader.
	 * @private
	 */
	load() {
		this.state.init();
		let doneCells = this.state.getDoneIds();
		if (doneCells.length) {
			doneCells
				.map(id=>document.getElementById(id))
				.filter(el=>el instanceof Element)
				.forEach(el=>{el.classList.add('done-cell')})
			;
		}
	}

	/**
	 * Reset all.
	 * @private
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