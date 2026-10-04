<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<h1>Orders</h1>
	<form action="<?php echo base_url('orders/create'); ?>" method="post">
		<label for="customer_name">Customer Name:</label>
		<input type="text" name="customer_name" id="customer_name" required>
		<br>
		<label for="product">Product:</label>
		<input type="text" name="product" id="product" required>
		<br>
		<label for="quantity">Quantity:</label>
		<input type="number" name="quantity" id="quantity" required>
		<br>
		<button type="submit">Create Order</button>
	</form>
	<h2>Existing Orders</h2>
	<table border="1">
		<tr>
			<th>ID</th>
			<th>Customer Name</th>
			<th>Product</th>		
		<th>Quantity</th>
		</tr>
		<?php foreach ($orders as $order): ?>
		<tr>
			<td><?php echo $order->id; ?></td>
			<td><?php echo $order->customer_name; ?></td>
			<td><?php echo $order->product; ?></td>
			<td><?php echo $order->quantity; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	
</body>
</html>
