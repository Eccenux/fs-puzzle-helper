/**
 * Copy helper.
 */
class ClipboardHelper {
	constructor() {

	}

	/**
	 * Copy text field contents.
	 * @param {Element|String} source Element or selector.
	 */
	static copyTextField(source) {
		if (typeof source === 'string') {
			source = document.querySelector(source);
		}
		source.select();
		document.execCommand("copy");
	}

}

export {ClipboardHelper}