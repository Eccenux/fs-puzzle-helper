import {EventsHelper} from './EventsHelper.js';

/**
 * Zoomer view handler.
 */
class ZoomerViewModel {
	constructor() {
		/**
		 * Zoomer main container element.
		 */
		this.mainContainer = null;
		/**
		 * Zoomer img list container element.
		 */
		this.listContainer = null;
		/**
		 * Id set of the img list.
		 */
		this.idList = new Set();
	}

	/**
	 * Init interactions.
	 * @param {String} columnsSelector CSS selector for all columns.
	 */
	init() {
		this.mainContainer = document.querySelector('#zoomer');
		this.listContainer = document.querySelector('#zoomer-list');
		this.initZoom();

		document.querySelector('#toggle-zoomer').addEventListener('click', ()=>{
			document.body.classList.toggle('hidden-zoomer');
		});

		this.initList();
	}

	/**
	 * Init list elements.
	 */
	initList() {
		let controls = document.querySelector('#zoomer-list-controls');
		this.initListClear(controls);
		this.initListResize(controls);
		this.listControls = controls;
		this.listControls.style.display = 'none';
	}

	/**
	 * Enlarge image.
	 */
	initZoom() {
		this.mainFig = document.querySelector('#zoomer .main');
		this.mainImg = this.mainFig.querySelector('img');
		this.mainCaption = document.querySelector('figcaption');
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('click', (e)=>{
				if (!e.ctrlKey) {
					// single/main image show
					this.cellLoad(img);
				} else {
					// add/remove from list
					this.mainFig.style.display = 'none';
					this.cellToggle(img);
				}

				// visibility of controls
				if (this.idList.size < 1) {
					this.listControls.style.display = 'none';
				} else {
					this.listControls.style.display = '';
				}
			});
		});	
	}

	/**
	 * Load cell image to main view.
	 */
	cellLoad(img) {
		this.mainImg.src = img.src;
		this.mainCaption.textContent = img.title;
		this.mainFig.style.display = '';

		// maybe later
		// containment don't work well when layout change
		// aspectRatio is not automatically update (would have to recalculate)
		/**
		//$('#zoomer .main img').resizable("destroy");
		$('#zoomer .main img').resizable({
			aspectRatio: true,
			containment: "#zoomer",
			minHeight: 150,
			minWidth: 200,
			//handles: "e, se",
			handles: "w, e",
		});
		/**/
	}

	/**
	 * Init list clear.
	 * @param {Element} container Controls container.
	 */
	initListClear(container) {
		container.querySelector('.clear').addEventListener('click', ()=>{
			this.idList.clear();
			this.listControls.style.display = 'none';
			this.listContainer.querySelectorAll(`.list`).forEach(element => {
				this.listContainer.removeChild(element);
			});
			document.querySelectorAll('.column img.zoomer-list').forEach(img => {
				img.classList.remove('zoomer-list');
			});
		});
	}

	/**
	 * Append or remove image from zoomer list.
	 * @param {Element} img Column image.
	 */
	cellToggle(img) {
		const id = img.id;
		if (this.idList.has(id)) {
			this.idList.delete(id);
			let el = this.listContainer.querySelector(`.list.${id}`);
			if (el) {
				this.listContainer.removeChild(el);
			} else {
				console.warn(`Zoomer list element not found ${id}`);
			}
			img.classList.remove('zoomer-list');
		} else {
			img.classList.add('zoomer-list');
			this.idList.add(id);
			let nel = document.createElement('figure');
			nel.innerHTML = `<img src='${img.src}'><figcaption>${img.title}</figcaption>`;
			nel.className = `list ${id}`;
			this.listContainer.appendChild(nel);

			// allow zoom-in from list
			nel.setAttribute('data-id', id);
			EventsHelper.clickDraggable(nel, () => {
				let id = nel.getAttribute('data-id');
				let img = document.getElementById(id);
				this.cellLoad(img);
			});
		}
	}

	/**
	 * Init resizing buttons.
	 * @param {Element} container Controls container.
	 */
	initListResize(container) {
		const resizers = container.querySelectorAll('.resize');
		const classes = [...resizers].map(el=>el.getAttribute('data-class'));
		resizers.forEach(button => {
			button.addEventListener('click', ()=>{
				let resizeClass = button.getAttribute('data-class');
				this.mainContainer.classList.remove(...classes);
				this.mainContainer.classList.add(resizeClass);
			});
		})
	}

}

export { ZoomerViewModel }