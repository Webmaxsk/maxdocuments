<% if SortedDocuments %>
	<div class="typography">
		<% if DocumentsContent %>
			<div class="content">
				$DocumentsContent
			</div>
		<% end_if %>
		<table>
			<% loop SortedDocuments %>
				<tr>
					<td><a href="$Url" download="">$Title</a></td>
					<td>$Size</td>
				</tr>
			<% end_loop %>
		</table>
	</div>
<% end_if %>