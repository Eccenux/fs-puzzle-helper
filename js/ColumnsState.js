import {StateStore} from './StateStore.js';

/**
 * Basic state operations.
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
		els.forEach((column)=>{
			this.columnMap[column.id] = column;
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