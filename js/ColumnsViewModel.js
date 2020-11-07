import {ColumnsState} from './ColumnsState.js';

/**
 * Columns view handler.
 */
class ColumnsViewModel {
	constructor() {
		/**
		 * State manager.
		 */
		this.state = new ColumnsState();
		/**
		 * Zoomer container element.
		 */
		this.zoomer = null;
		/**
		 * Zoomer images list.
		 */
		this.zoomerList = new Set();
	}
	/**
	 * Init interactions.
	 * @param {String} columnsSelector CSS selector for all columns.
	 */
	init() {
		this.state.init('.column');
		this.initZoomer();
		this.initDoneToggle();
		this.initReset();
	}

	/**
	 * Enlarge col.
	 */
	initZoomer() {
		this.zoomer = document.querySelector('#zoomer');
		let mainImg = document.querySelector('#zoomer img');
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('click', (e)=>{
				if (!e.ctrlKey) {
					mainImg.src = img.src;
					mainImg.style.display = '';
				} else {
					mainImg.style.display = 'none';
					this.zoomerToggle(img);
				}
				// mark active (current)
				document.querySelectorAll('#columns .column.active').forEach((prev)=>{
					prev.classList.remove('active');
				})
				img.parentNode.classList.add('active');
			});
		});	
	}

	/**
	 * Append or remove image from zoomer list.
	 * @param {Element} img Column image.
	 */
	zoomerToggle(img) {
		const id = img.id;
		if (this.zoomerList.has(id)) {
			this.zoomerList.delete(id)
			let el = this.zoomer.querySelector(`.list.${id}`);
			if (el) {
				this.zoomer.removeChild(el);
			} else {
				console.warn(`Zoomer list element not found ${id}`);
			}
			img.classList.remove('zoomer-list');
		} else {
			img.classList.add('zoomer-list');
			this.zoomerList.add(id);
			let nel = document.createElement('img');
			nel.className = `list ${id}`;
			nel.src = img.src;
			this.zoomer.appendChild(nel);
		}
	}

	/**
	 * Show/hide col.
	 */
	initDoneToggle() {
		document.querySelectorAll('.column h2').forEach(head=>{
			head.addEventListener('click', ()=>{
				this.state.toggleDone(head.parentNode);
			});
		});
	}

	/**
	 * Reset all.
	 */
	initReset() {
		document.querySelector('#reset-all').addEventListener('click', ()=>{
			let confirmed = confirm('Reset all columns?');
			if (confirmed) {
				this.state.resetAll();
			}
		});
	}
}

export { ColumnsViewModel }