/**
 * CSS variables helper.
 */
class CssVariables {
	/**
	 * Get :root variable.
	 * @param {String} name Name of CSS var. e.g. "--columns-shown".
	 * @returns {String} Current value.
	 */
	static getRootVar(name) {
		return this.getVar(document.documentElement, name);
	}
	/**
	 * @see #getRootVar
	 */
	static getVar(element, name) {
		return window.getComputedStyle(element, null).getPropertyValue(name);
	}
	/**
	 * Set :root variable.
	 * @param {String} name Name of CSS var. e.g. "--columns-shown".
	 * @param {String} value New value. Should be a strin, but numbers would work as well.
	 */
	static setRootVar(name, value) {
		this.setVar(document.documentElement, name, value);
	}
	/**
	 * @see #setRootVar
	 */
	static setVar(element, name, value) {
		element.style.setProperty(name, value);
	}
}

export { CssVariables }