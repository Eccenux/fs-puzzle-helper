/**
 * Basic state operations.
 */
class ColumnsState {
	constructor() {
		/**
		 * Storage key.
		 */
		this.stateKey = 'cols-state';
		/**
		 * Id to element map.
		 */
		this.columnMap = {};

		// pre-reset
		this.propReset();
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
			columnMap[column.id] = column;
		});

		// todo: restore DOM state (add classes)
	}
	/**
	 * Reset state properties.
	 * @private
	 */
	propReset() {
		this.count = 0;
		this.state = [];
	}
	readAll() {
		let stateJson = localStorage.getItem(this.stateKey);
		if (stateJson == null || !Array.isArray(stateJson)) {
			this.propReset();
		} else {
			this.state = JSON.parse(stateJson);
		}
	}
	writeAll() {
		localStorage.setItem(this.stateKey, JSON.stringify(this.state));
	}
}
