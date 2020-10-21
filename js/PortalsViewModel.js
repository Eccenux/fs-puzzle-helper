//import {PortalsState} from './PortalsState.js';

/**
 * Portals view handler.
 */
class PortalsViewModel {
	constructor() {
		/**
		 * State manager.
		 */
		//this.state = new PortalsState();
	}
	/**
	 * Init interactions.
	 * @param {String} PortalsSelector CSS selector for all Portals.
	 */
	init() {
		//this.state.init('.column');
		this.initDoneMarks();
	}

	/**
	 * Adding done-marks.
	 */
	initDoneMarks() {
		document.querySelectorAll('.column').forEach(column=>{
			column.addEventListener('contextmenu', function(event) {
				event.preventDefault();
				console.log('done', this, event);
				//this.state.toggleDone(head.parentNode);
			});
		});
	}
}

export { PortalsViewModel }