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
	 * Find portal by ID.
	 * 
	 * @private
	 * @param {String} id Cell id.
	 */
	findPortal(id) {
		let existingPortals = this.portals
			.filter(portal=>portal.id === id)
		;
		if (existingPortals.length) {
			return existingPortals[0];
		}
		return null;
	}

	/**
	 * Set as done.
	 * @param {PortalCell} cell 
	 */
	setDone(cell) {
		// set or create
		let portal = this.findPortal(cell.id);
		if (portal !== null) {
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
	setUnDone(id) {
		let portal = this.findPortal(id);
		if (portal !== null) {
			portal.done = false;
		}
		this.writeAll();
	}
	/**
	 * Get portal.
	 * @param {PortalCell} cell 
	 */
	getPortal(cell) {
		// use or create
		let portal = this.findPortal(cell.id);
		if (portal === null) {
			portal = new Portal(cell);
		}
		return portal;
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