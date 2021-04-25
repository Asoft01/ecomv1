<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Email</title>
</head>
<body>
    <table width="700px">
        <tr>
            <td></td>
        </tr>
        <tr>
            <td><img src="{{ asset('images/frontend_images/home/logo.png') }}" alt="Image Logo is here"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Hello {{ $name }},</td>
        </tr>
        
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Thank You for shopping with us. Your Order details are as below: </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Order No: {{ $order_id }}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="95%" cellpadding="5" bgcolor="#f7f4f4">
                    <tr bgcolor="#cccccc">
                        <td>Product Name</td>
                        <td>Product Code</td>
                        <td>Size</td>
                        <td>Color</td>
                        <td>Quantity</td>
                        <td>Unit Price</td>
                    </tr>
                    @foreach ($productDetails['orders'] as $product)
                        <tr>
                            <td>{{ $product['product_name'] }}</td>
                            <td>{{ $product['product_code'] }}</td>
                            <td>{{ $product['product_size'] }}</td>
                            <td>{{ $product['product_color'] }}</td>
                            <td>{{ $product['product_qty'] }}</td>
                            <td>INR {{ $product['product_price'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" align="right">Shipping Charges</td>
                        <td>INR {{ $productDetails['shipping_charges'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right">Coupon Discount</td>
                        <td>INR {{ $productDetails['coupon_amount'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right">Grand Total</td>
                        <td>INR {{ $productDetails['grand_total'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <table>
                                <tr>
                                    <td><strong>Bill To:-</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['name'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['address'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['city'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['state'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['country'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['pincode'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['mobile'] }}</td>
                                </tr>
                                
                            </table>
                        </td>
                        <td width="50%">
                            <table>
                                <tr>
                                    <td><strong>Ship To:-</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['name'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['address'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['city'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['state'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['country'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['pincode'] }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $userDetails['mobile'] }}</td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>For any enquiries, you can contact us at <a href="mailto:info@ecom-website">info@ecom-website.com</a></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>Regards, <br>Team E-Com</td></tr>
        <tr><td>&nbsp;</td></tr>
    </table>
</body>
</html>