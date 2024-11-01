<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="font-family: 'Roboto', sans-serif; background: #F5F5F5;line-height:1.3">
	<div>
		<div style="background:#fff; padding: 20px; max-width:650px;width:80%; margin: 10px auto; font-weight: 500;  ">
			<table>
				<tr>
					<table>
						<tr>
							<td>
								<p>Hello there,<br>
									Thank you for contacting our Support team. This is an automated email is sent to
									confirm support case has been created from your recent chat.</p>
								<p>Here are the details of your recent chat with us:</p>
							</td>
						</tr>
					</table>
				</tr>

						<tr>
							<td>
							<p style="margin: 0px ; padding-bottom: 15px"><b><?php esc_attr_e( 'Chat Started On:', 'wschat' ); ?></b> <?php echo esc_attr( $start_time ); ?></p>
<hr />
							</td>
						</tr>
				<tr>
					<table>

						<?php foreach ( $messages->reverse()  as $message ) : ?>
							<?php if ( 'text' === $message['type'] ) : ?>
							<tr>
								<td>
									<p style="margin: 0;padding-bottom: 10px"><?php echo esc_html( $message['user']['name'] ); ?>: <?php echo wp_kses_post( nl2br( $message['body']['text'] ) ); ?></p>
									<?php if ( isset( $message['body']['attachments'] ) ) : ?>
										<?php foreach ( $message['body']['attachments'] as $attachment ) : ?>
											<a
												href="<?php echo esc_url( $attachment['url'] ); ?>"
												style="color:#1592E6; display:inline-block; padding-bottom: 15px; word-break: break-word;"
											><?php echo esc_url( $attachment['url'] ); ?></a>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
							</tr>
							<?php else : ?>
								<tr>
									<td>
										<p style="margin: 0px ; padding-bottom: 15px"><?php echo esc_html( $message['body']['text'] ); ?></p>
									</td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					</table>
				</tr>

				<tr>
					<td>
<hr />
						<p><?php esc_attr_e( 'Thank You For Writing to Us', 'wschat' ); ?></p>
					</td>
				</tr>

				<tr>
					<td>
					<p style="text-align: center; "><?php esc_attr_e( 'Powered By:', 'wschat' ); ?><a href="" style="color:#1592E6">WSChat</a></p>
					</td>
				</tr>
			</table>
		</div>
	</div>

</body>

</html>
