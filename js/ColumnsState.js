import {StateStore} from './StateStore.js';
import {CssVariables} from './CssVariables.js';

/**
 * Basic state operations.
 * 
 * @todo This state is a bit too much like viewmodel... Should probably remove elements operations.
 */
class ColumnsState {
	constructor() {
		/**
		 * Storage key.
		 */
		this.store = new StateStore('puzzle-cols-state');
		/**
		 * Id to element map.
		 */
		this.columnMap = {};
		/**
		 * Done ids list.
		 */
		this.done = new Set();
	}
	/**
	 * Init page based on stored state.
	 * @param {String} columnsSelector CSS selector for all columns.
	 */
	init(columnsSelector) {
		// read state
		this.readAll();

		let els = document.querySelectorAll(columnsSelector);
		
		// map ids to elements
		this.columnMap = {};
		this.columnCount = 0;
		els.forEach((column)=>{
			this.columnMap[column.id] = column;
			this.columnCount++;
		});
		//console.log('init', this.done);

		// restore DOM state (add classes)
		for (let id of this.done) {			
			if (!(id in this.columnMap)) {
				continue;
			}
			let column = this.columnMap[id];
			column.classList.add('done');
		}

		// init CSS
		this.zoomerBaseWidth = parseInt(CssVariables.getRootVar("--zoomer-width").replace('px', ''));
		this.cssVariables();
	}
	cssVariables() {
		let doneCount = this.done.size;
		const shown = this.columnCount - doneCount;
		CssVariables.setRootVar('--columns-shown', shown);
		CssVariables.setRootVar('--columns-hidden', doneCount);
		// add more space for zoomer when there is space
		const zoomTrigger = 7;
		if (shown < zoomTrigger) {
			let winWidth = window.outerWidth;
			let zoomerLimit = winWidth * 0.3;
			let newWidth = this.zoomerBaseWidth + (zoomTrigger - shown) * 100;
			if (newWidth > zoomerLimit) {
				newWidth = zoomerLimit;
			}
			CssVariables.setRootVar('--zoomer-width', `${newWidth}px`);
		} else {
			CssVariables.setRootVar('--zoomer-width', `${this.zoomerBaseWidth}px`);
		}
		// debug
		// //CssVariables.setRootVar('--column-max-width', 'calc((90vw - var(--zoomer-width)) / (var(--columns-shown) + 1))');
		// console.log('width:', CssVariables.getRootVar("--column-max-width"));
		// console.log('shown:', CssVariables.getRootVar("--columns-shown"));
		// console.log('hidden:', CssVariables.getRootVar("--columns-hidden"));
	}
	/**
	 * Toggle state of a column.
	 * @param {Element} column 
	 */
	toggleDone(column) {
		// toggle DOM state
		let isDone = column.classList.toggle('done');
		// save state
		if (isDone) {
			this.done.add(column.id);
		} else {
			this.done.delete(column.id);
		}
		this.writeAll();
	}
	/**
	 * Reset state.
	 */
	resetAll() {
		// reset DOM state
		for (let id of this.done) {			
			if (!(id in this.columnMap)) {
				continue;
			}
			let column = this.columnMap[id];
			column.classList.remove('done');
		}
		// save state
		this.done.clear();
		this.writeAll();
	}

	/**
	 * Write state to storage.
	 * @private
	 */
	writeAll() {
		this.cssVariables();
		this.store.write({
			done: [...this.done],
		});
	}

	/**
	 * Read state data.
	 * @private
	 */
	readAll() {
		let state = this.store.read();
		if (state != null && 'done' in state && Array.isArray(state.done)) {
			this.done = new Set(state.done);
		} else {
			this.done.clear();
		}
	}
}

export { ColumnsState }