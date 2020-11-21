/**
 * Events helper.
 */
class EventsHelper {
	/**
	 * Add listener for click for draggable elements.
	 * 
	 * Click callback will only be called when drag was not detected.
	 * 
	 * @param {Element} element 
	 * @param {Function} callback 
	 */
	static clickDraggable(element, callback) {
		let dragged = false;
		let dragDetect = function() {
			window.addEventListener('mousemove', () => {
				dragged = true;
				window.removeEventListener("mousemove", dragDetect);
			});
		}
		element.addEventListener('mousedown', () => {
			dragged = false;
			dragDetect();
		});
		element.addEventListener('mouseup', () => {
			if (!dragged) {
				callback();
			}
		});
	}
}

export { EventsHelper }