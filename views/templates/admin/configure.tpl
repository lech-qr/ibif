<div class="panel">
    <form action="{$currentIndex}&configure={$name}&token={$token}" method="post" enctype="multipart/form-data">
        <input type="hidden" style="display: inline-block; width: 100%" name="HOMEPAGEBOXES" id="HOMEPAGEBOXES" value="{$boxes_json|escape:'html'}">

        <button type="button" id="add-box" class="btn btn-primary"><i class="icon-plus"></i>&nbsp;&nbsp;{l s='Add Box'}</button>
        <button type="submit" value="{l s='Save'}" name="submit{$name}" class="btn btn-success pull-right"><i class="icon-save"></i>&nbsp;&nbsp;{l s='Save'}</button>

        <div id="boxes-list" class="row">
        </div>

        <br><br>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let boxes = JSON.parse(document.getElementById('HOMEPAGEBOXES').value);
        let container = document.getElementById('boxes-list');

        function createBoxElement(box) {
	    // Display existing boxes
            let boxDiv = document.createElement('div');
            boxDiv.classList.add('box', 'col-md-6', 'col-sm-12');
            boxDiv.setAttribute('data-id', box.id_box);

            let innerBox = document.createElement('div');

            let titleH1 = document.createElement('h3');
            titleH1.innerHTML = box.title;

            let titleInput = document.createElement('input');
            titleInput.type = 'text';
            //titleInput.name = 'box_title';
            titleInput.value = box.title;
            titleInput.placeholder = '{l s="Box Title"}';

            let imgInput = document.createElement('input');
            //imgInput.name = 'box_background_image';
            imgInput.type = 'file';

            let imgHiddenInput = document.createElement('input');
            imgHiddenInput.type = 'hidden';

            let linkSelect = document.createElement('select');
            //linkSelect.name = 'box_link';

            let categories = JSON.parse({$categories|json_encode});
            let products = JSON.parse({$products|json_encode});
            let cmsPages = JSON.parse({$cms_pages|json_encode});

            console.log(JSON.parse({$products|json_encode}))

            linkSelect.appendChild(new Option('{l s="Select Link"}', ''));

            let optgroupCategory = document.createElement('optgroup');
            optgroupCategory.label = '{l s="Categories"}';
            categories.forEach(function(category) {
                let option = new Option(category.name, category.id_category + '-' + category.link_rewrite);
                if (box.link === category.id_category + '-' + category.link_rewrite) {                  
                    option.selected = true;
                }
                optgroupCategory.appendChild(option);
            });
            linkSelect.appendChild(optgroupCategory);

            let optgroupProduct = document.createElement('optgroup');
            optgroupProduct.label = '{l s="Products"}';
            products.forEach(function(product) {
                let option = new Option(product.name, product.id_product + '-' + product.link_rewrite + '.html');
                if (box.link === product.id_product + '-' + product.link_rewrite + '.html') {
                    option.selected = true;
                }
                optgroupProduct.appendChild(option);
            });
            linkSelect.appendChild(optgroupProduct);

            let optgroupCMS = document.createElement('optgroup');
            optgroupCMS.label = '{l s="CMS Pages"}';
            cmsPages.forEach(function(page) {
                let option = new Option(page.meta_title, 'content/' + page.id_cms + '-' + page.meta_title.toLowerCase().replace(/\s+/g, "-"));
                if (box.link === 'content/' + page.id_cms + '-' + page.meta_title.toLowerCase().replace(/\s+/g, "-")) {
                    option.selected = true;
                }
                optgroupCMS.appendChild(option);
            });
            linkSelect.appendChild(optgroupCMS);

            //let removeButton = document.createElement('button');
            //removeButton.type = 'button';
            //removeButton.classList.add('btn', 'btn-danger', 'pull-right');
            //removeButton.innerHTML = '{l s="Remove"}';
            //removeButton.addEventListener('click', function() {
            //    container.removeChild(boxDiv);
            //});

            boxDiv.appendChild(innerBox);

            innerBox.appendChild(titleH1);
            if (box.background_image) {
                let imgPreview = document.createElement('img');
                imgPreview.src = '/ibif/' + box.background_image;
                imgPreview.style.width = 'auto';
                imgPreview.style.height = '100px';
                innerBox.appendChild(imgPreview);
            }             
            innerBox.appendChild(titleInput);
            innerBox.appendChild(imgInput);
            innerBox.appendChild(imgHiddenInput);
            innerBox.appendChild(linkSelect);
            //innerBox.appendChild(removeButton);           

            container.appendChild(boxDiv);
        }

        boxes.forEach(function(box) {
            createBoxElement(box);
        });

        document.getElementById('add-box').addEventListener('click', function() {
            $('.box').remove(); 
            createBoxElement({ title: '', background_image: '', link: '' });
            $('.box input:nth-of-type(1)').attr('name', 'box_title'); 
            $('.box input:nth-of-type(2)').attr('name', 'box_background_image');
            $('.box select:nth-of-type(1)').attr('name', 'box_link');            
            $('.box h3').remove();
            $('.box .btn-danger').remove();
            boxes.forEach(function(box) {
                createBoxElement(box);
            });            
        });

        document.forms[0].addEventListener('submit', function() {
            let boxElements = container.querySelectorAll('.box');
            let boxes = [];
            boxElements.forEach(function(boxElement) {
                let title = boxElement.querySelector('input[name="box_title"]').value;
                let background_image = boxElement.querySelector('input[name="box_background_image_hidden"]').value;
                let link = boxElement.querySelector('select[name="box_link"]').value;
                boxes.unshift({ title: title, background_image: background_image, link: link });
            });
            document.getElementById('HOMEPAGEBOXES').value = JSON.stringify(boxes);
        });

    });
</script>

<style>
    .panel {
        display: flow-root; 
    }
    .box > div {
        display: flow-root;       
        margin: 1rem .5rem;
        padding: 2rem;
        border: 1px solid #ccc;
    }
    .box h3 {
        margin: 0 0 1rem 0;
    }
    .box input, .box select {
        margin-bottom: 1rem;
    }
    .bootstrap .btn  {
        text-transform: uppercase;
        font-size: 1rem;
    }
    .bootstrap .btn [class^="icon-"] {
        font-size: 1rem;
    }
</style>