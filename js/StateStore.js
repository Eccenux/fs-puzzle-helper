/**
 * State storage helper.
 * 
 * Synchronous.
 */
class StateStore {
	constructor(stateKey) {
		/**
		 * Storage key.
		 */
		this.stateKey = stateKey;
	}
	/**
	 * Write state to storage.
	 * @param {Object} state Any plain object.
	 */
	write(state) {
		localStorage.setItem(this.stateKey, JSON.stringify(state));
	}
	/**
	 * Read state from storage.
	 * @returns {Object} state Plain object.
	 */
	read() {
		let state = null;
		let stateJson = localStorage.getItem(this.stateKey);
		if (stateJson != null) {
			try {
				state = JSON.parse(stateJson);
			} catch (error) {
				console.error('Read error', error);
			}
		}
		return state;
	}
}

export { StateStore }