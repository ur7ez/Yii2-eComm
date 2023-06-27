$(function (){
    const cartQuantity = $('#cart-quantity');
    const addToCart = $('.btn-add-to-cart');
    const itemQuantities = $('.item-quantity');

    addToCart.click(ev => {
        ev.preventDefault();
        const $this = $(ev.target);
        const id = $this.closest('.product-item').data('key');
        $.ajax({
            method: 'POST',
            url: $this.attr('href'),
            data: {id},
            success: function () {
                cartQuantity.text(parseInt(cartQuantity.text() || 0) + 1);
            }
        });
    });

    itemQuantities.change(ev => {
        const $this = $(ev.target);
        let $tr = $this.closest('tr'),
            $td = $this.closest('td');
        const id = $tr.data('id');
        let quantity = $this.val();
        if (quantity < 1) {
            quantity = 1;
            $this.val(1);
        }
        $.ajax({
            method: 'POST',
            url: $tr.data('url'),
            data: {id, quantity},
            success: function (res) {
                cartQuantity.text(res.quantity);
                $td.next().text(res.item_price);
            }
        });
    });
});