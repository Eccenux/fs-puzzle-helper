/**
 * Portal-Cell model.
 */
class PortalCell {

	/**
	 * new
	 */
	constructor() {
		this.id = '';
		this.col = 1;
		this.row = 1;
	}

	/**
	 * Cell from img.
	 * @param {Element} img Cell image.
	 */
	static fromImage(img) {
		let cell = new PortalCell();
		cell.id = img.id;
		cell.col = img.getAttribute('data-col');
		cell.row = img.getAttribute('data-row');
		return cell;
	}

	/**
	 * Get cell url.
	 * @param {Element} img Cell image.
	 */
	static readUrl(img) {
		// data-url for transformed images
		let url = img.getAttribute('data-url');
		if (typeof url === 'string' && url.length) {
			return url;
		}
		// fallback
		return img.src;
	}
}

export { PortalCell };
