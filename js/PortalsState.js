import {StateStore} from './StateStore.js';
import {Portal} from './Portal.js';
import {PortalCell} from './PortalCell.js';

/**
 * Basic state operations.
 */
class PortalsState {
	constructor() {
		/**
		 * Storage key.
		 */
		this.store = new StateStore('puzzle-portals-state');
		/**
		 * Portals list (of Portal type).
		 */
		this.portals = [];
	}
	/**
	 * Init (or re-init) state on load.
	 */
	init() {
		// read state
		this.readAll();
	}
	/**
	 * Reset state.
	 */
	resetAll() {
		// save state
		this.portals = [];
		this.writeAll();
	}
	/**
	 * Get id list of done portals.
	 */
	getDoneIds() {
		return this.portals
			.filter(portal=>portal.done)
			.map(portal=>portal.id)
		;
	}
	/**
	 * Set as done.
	 */
	setDone(cell) {
		let existingPortals = this.portals
			.filter(portal=>portal.id === cell.id)
		;
		// set or create
		let portal;
		if (existingPortals.length) {
			portal = existingPortals.pop();
			portal.done = true;
		} else {
			portal = new Portal(cell, true);
			this.portals.push(portal);
		}
		this.writeAll();
	}
	/**
	 * UnSet done state.
	 */
	setUnDone(cell) {
		let existingPortals = this.portals
			.filter(portal=>portal.id === id)
		;
		// set or create
		let portal;
		if (existingPortals.length) {
			portal = existingPortals.pop();
		} else {
			portal = new Portal(cell);
		}
	}

	/**
	 * Write state to storage.
	 * @private
	 */
	writeAll() {
		// Note! Assuming store will clone portals.
		this.store.write({
			portals: this.portals,
		});
	}

	/**
	 * Read state data.
	 * @private
	 */
	readAll() {
		let state = this.store.read();
		if (state != null && 'portals' in state && Array.isArray(state.portals)) {
			// TODO: should probably re-write to Portal class.
			this.portals = state.portals;
		} else {
			this.portals = [];
		}
	}
}

export { PortalsState }