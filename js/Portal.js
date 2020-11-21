import {PortalCell} from './PortalCell.js';

const baseUrl = 'https://intel.ingress.com/intel';

/**
 * Portal model.
 */
class Portal {

	/**
	 * Portal constructor.
	 * @param {PortalCell} cell The cell.
	 * @param {boolean} done (optional) Initial state (defaults to off).
	 */
	constructor(cell, done) {
		// base data
		this.done = done ? true : false;
		this.col = cell.col;
		this.row = cell.row;
		/**
		 * Element id.
		 */
		this.id = cell.id;

		// solvable data
		/**
		 * Actual portal title.
		 */
		this.title = '';
		/**
		 * Solver notes.
		 */
		this.notes = '';
		this.discoverer = '';
		/**
		 * Location. Note! Use `setLocation`.
		 * Do NOT set directly.
		 * @private
		 */
		this._l =  {
			lat: '',
			lon: '',
		};
	}

	/**
	 * Create new portal from dummped data.
	 * @param {Portal} obj Dummped data with at least similar props.
	 */
	static create(obj) {
		if (!(obj && 'col' in obj && 'row' in obj && 'id' in obj)) {
			console.error('Portal data is invalid', obj);
			return null;
		}
		let portal = new Portal(obj);
		portal.title = obj.title;
		portal.notes = obj.notes;
		portal.discoverer = obj.discoverer;
		portal.setLocation(obj._l.lat, obj._l.lon);
		return portal;
	}

	/**
	 * Set location for portal.
	 * @param {String} latitude Latitude string.
	 * @param {String} longitude Longitude string.
	 */
	setLocation(latitude, longitude) {
		this._l.lat = latitude;
		this._l.lon = longitude;
	}

	/**
	 * Puzzle TSV data.
	 * 
	 * Example (nickname is optional):
	 * `Директорский Дом	eccenux	https://intel.ingress.com/intel?ll=55.922126,37.809053&z=17&pll=55.922126,37.809053`
	 */
	puzzleData() {
		let url = this.getUrl();
		return `${this.title}\t${this.discoverer}\t${url}`;
	}

	/**
	 * Get portal URL.
	 */
	getUrl() {
		let ll = this.getLatLon();
		return `${baseUrl}?ll=${ll}&z=17&pll=${ll}`;
	}

	/**
	 * Get typical lat-lon combo.
	 */
	getLatLon() {
		return `${this._l.lat},${this._l.lon}`;
	}

}

export { Portal };
